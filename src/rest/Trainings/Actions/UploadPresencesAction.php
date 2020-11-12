<?php

declare(strict_types=1);

namespace REST\Trainings\Actions;

use Cake\Database\Expression\QueryExpression;
use Judo\Domain\Member\MembersTable;
use Judo\Domain\Member\MemberTransformer;
use Kwai\Core\Infrastructure\Presentation\Responses\JSONResponse;
use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Psr7\UploadedFile;

class UploadPresencesAction
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $files = $request->getUploadedFiles();
        if (!isset($files['xls'])) {
            return $response->withStatus(400);
        }

        if (is_array($files['xls'])) {
            $uploadedFiles = $files['xls'];
        } else {
            $uploadedFiles = [ $files['xls'] ];
        }

        $result = [];
        foreach ($uploadedFiles as $uploadedFile) {
            $result[]
                = $this->processFile($uploadedFile);
        }

        return (new JSONResponse($result))($response);
    }

    private function processFile(UploadedFile $upload): object
    {
        $result = (object)[
            'filename' => $upload->getClientFilename()
        ];

        // Try to retrieve the date from the filename
        // Check format DD-MM-YYYY or YYYY-MM-DD
        if (preg_match('/(\d{2})-(\d{2})-(\d{4})/', $upload->getClientFilename(), $match)) {
            $result->date = $match[3] . '-' . $match[2] . '-' . $match[1];
        } elseif (preg_match('/(\d{4})-(\d{2})-(\d{2})/', $upload->getClientFilename(), $match)) {
            $result->date = $match[1] . '-' . $match[2] . '-' . $match[3];
        }
        $fileType = IOFactory::identify($upload->getFilePath());
        if (! isset($fileType)) {
            $result->error = 'Cannot identify the filetype';
            return $result;
        }
        $result->filetype = strtolower($fileType);

        try {
            $reader = IOFactory::createReader($fileType);
        } catch (Exception $e) {
            $result->error = 'No reader available';
            return $result;
        }

        $reader->setReadDataOnly(true);
        $sheet = IOFactory::load($upload->getFilePath());
        $result->rows = $sheet->getActiveSheet()->getHighestRow('A');

        $licenses = [];
        foreach ($sheet->getActiveSheet()->getRowIterator(1, $result->rows) as $row) {
            $firstColumnValue = $row->getCellIterator('A', 'A')
                ->current()
                ->getValue()
            ;
            // Only allow numeric values, and remove leading zeroes
            if (preg_match('/^[0-9]+$/', $firstColumnValue)) {
                $licenses[] = ltrim($firstColumnValue, '0');
            }
        }
        $result->licenses = $licenses;

        if (count($licenses) === 0) {
            $result->members = [
                'data' => []
            ];
            return $result;
        }
        $membersTable = MembersTable::getTableFromRegistry();
        $query = $membersTable
            ->find()
            ->contain(['Person', 'Person.Contact', 'Person.Nationality'])
            ->where(function (QueryExpression $exp, $q) use ($licenses) {
                return $exp->in('license', $licenses);
            })
        ;
        $members = $query->all();
        $resource = MemberTransformer::createForCollection($members);
        $fractal = new Manager();
        $fractal->setSerializer(new JsonApiSerializer());
        $result->members = $fractal->createData($resource)->toArray();

        return $result;
    }
}

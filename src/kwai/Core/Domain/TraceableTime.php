<?php
/**
 * TraceableTime class
 */
 declare(strict_types = 1);

 namespace Kwai\Core\Domain;

/**
  * Value object that handles the created_at and updated_at timestamps.
  */
 final class TraceableTime
 {
     /**
      * Time of creation
      * @var Kwai\Core\Domain\DateTime
      */
     private $created_at;

     /**
      * Time of last update
      * @var Kwai\Core\Domain\DateTime
      */
     private $updated_at;

     /**
      * Constructor
      *
      * @param DateTime $created_at The timestamp of creation
      * @param DateTime $updated_at The timestamp of the last modification
      */
     public function __construct(
         DateTime $created_at = null,
         DateTime $updated_at = null
     ) {
         $this->created_at = $created_at ?? DateTime::createNow();
         $this->updated_at = $updated_at;
     }

     /**
      * Returns the timestamp of creation.
      * @return DateTime
      */
     public function created_at(): DateTime
     {
         return $this->created_at;
     }

     /**
      * Returns the timestamp of the last modification
      * @return DateTime
      */
     public function updated_at(): DateTime
     {
         return $this->created_at;
     }
 }

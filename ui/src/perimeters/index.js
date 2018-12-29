import basePerimeter from './basePerimeter';
import authPerimeter from './authPerimeter';
import categoryPerimeter from './categoryPerimeter';
import newsPerimeter from './newsPerimeter';
import pagePerimeter from './pagePerimeter';
import memberPerimeter from './memberPerimeter';
import teamPerimeter from './teamPerimeter';
import teamTypePerimeter from './teamTypePerimeter';
import userPerimeter from './userPerimeter';
import seasonPerimeter from './seasonPerimeter';
import trainingDefinitionPerimeter from './trainingDefinitionPerimeter';
import trainingCoachPerimeter from './trainingCoachPerimeter';
import trainingEventPerimeter from './trainingEventPerimeter';

function perimeters() {
  return [
    basePerimeter,
    authPerimeter,
    categoryPerimeter,
    newsPerimeter,
    pagePerimeter,
    memberPerimeter,
    teamPerimeter,
    teamTypePerimeter,
    userPerimeter,
    seasonPerimeter,
    trainingDefinitionPerimeter,
    trainingCoachPerimeter,
    trainingEventPerimeter,
  ];
};

export default perimeters;

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
import eventPerimeter from './eventPerimeter';
import trainingDefinitionPerimeter from './trainingDefinitionPerimeter';
import trainingCoachPerimeter from './trainingCoachPerimeter';
import trainingPerimeter from './trainingPerimeter';

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
    eventPerimeter,
    trainingDefinitionPerimeter,
    trainingCoachPerimeter,
    trainingPerimeter,
  ];
};

export default perimeters;

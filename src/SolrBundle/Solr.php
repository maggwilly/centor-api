<?php

namespace SolrBundle;

use FS\SolrBundle\Query\AbstractQuery;

use FS\SolrBundle\Solr as BaseSolr;

/**
 * Class allows to index doctrine entities
 */
class Solr extends BaseSolr
{
    /**
     * Get select query
     *
     * @param AbstractQuery $query
     *
     * @return \Solarium\QueryType\Select\Query\Query
     */
    public function getSelectQuery(AbstractQuery $query)
    {
        $metaInformation= $query->getMetaInformation();
        $selectQuery = $this->solrClientCore->createSelect($query->getOptions());
        if($query->getMetaInformation()->isDoctrineEntity())
           $selectQuery->setFilterQueries($query->getFilterQueries());
        $selectQuery->setQuery($query->getQuery());
        $selectQuery->setSorts($query->getSorts());
        $selectQuery->setFields($query->getFields());
        return $selectQuery;
    }
}

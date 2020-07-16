<?php


namespace SolrBundle;

use DateTime;
use JMS\Serializer\VisitorInterface;

class DateTimeInterfaceHandler
{
    public function serializeToJson(VisitorInterface $visitor, DateTime $dateTime, array $type)
    {
        return $dateTime->format(DATE_ATOM);
    }
}
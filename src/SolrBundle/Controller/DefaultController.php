<?php

namespace SolrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\Annotations as Rest;

// alias pour toutes les annotations
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use FS\SolrBundle\Doctrine\Hydration\HydrationModes;

// Utilisation de la vue de FOSRestBundle
class DefaultController extends Controller
{

    /**
     * @Route("/formated/search")
     * @Rest\View(serializerGroups={"full"})
     */
    public function indexAction(Request $request)
    {
        $entities = array(
            'Concours' => 'AppBundle:Session',
            'Ecole' => 'AppBundle:Concours',
            'Arrêté' => 'AppBundle:Resultat',
            'Annonce' => 'MessagerBundle:Notification',
            'Document' => 'AdminBundle:Ressource'
        );
        $entity_key = $request->get("entity_key");
        if (array_key_exists($entity_key, $entities)) {
            $query = $this->get('solr.client')->createQuery($entities[$entity_key]);
            if ($request->get("term"))
               $query->setCustomQuery('result_type_s:'.$request->get("entity_key").' AND _text_:'.$request->get("term").'*');
            return $query->getResult();
        }
        $query = $this->get('solr.client')->createQuery('SolrBundle:SolrSearchResult');
        if ($request->get("term"))
            $query->setCustomQuery('_text_:' . $request->get("term"));
        $query->setHydrationMode(HydrationModes::HYDRATE_INDEX);
        return $query->getResult();
    }
}

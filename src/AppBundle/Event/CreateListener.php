<?php

namespace AppBundle\Event;

use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\EventDispatcher\Event;
use Misteio\CloudinaryBundle\Wrapper\CloudinaryWrapper;
use Pwm\MessagerBundle\Entity\Notification;
use Pwm\MessagerBundle\Entity\Sending;
use AppBundle\Service\FMCManager;
use Doctrine\ORM\EntityManager;

class CreateListener
{
// Liste des id des utilisateurs à surveiller

    protected $cloudinaryWrapper;
    protected $_em;
    protected $twig;
    protected $fcm;

    public function __construct(CloudinaryWrapper $cloudinaryWrapper, EntityManager $_em, \Twig_Environment $templating, FMCManager $fcm)
    {
        $this->cloudinaryWrapper = $cloudinaryWrapper;
        $this->_em = $_em;
        $this->twig = $templating;
        $this->fcm = $fcm;
    }


    public function onQuestionCreated(QuestionEvent $event)
    {
        $question = $event->getQuestion();
        $image = $question->getImageEntity();
        if ($image != null) {
            if ($image->upload()) {
                $results = $this->cloudinaryWrapper->upload($image->getPath(), '_question_' . $question->getId(), array(), array("crop" => "limit", "width" => "200",
                    "height" => "150"))->getResult();
                $image->setUrl($results['url']);
                $this->_em->flush();
                $image->remove();
            }
        }
    }

    public function onRegistration(RegistrationEvent $event)
    {
        $registrations = array($event->getRegistration());
        $info = $event->getRegistration()->getInfo();
        $notification = $this->_em->getRepository('MessagerBundle:Notification')->findOneByTag('welcome_message');
        $notification->setIncludeMail(true);
        $this->_em->flush();
        $notification->setSousTitre("Entrez de plein pieds dans un univers d'opportunités qui vous surprendra à coup sûr...");
        $registrationIds = $this->sendTo($registrations);
        $data = array('page' => 'notification', 'id' => $notification->getId());
        $result = $this->sendToTokens($registrationIds, $notification, $data);
        $this->controlFake($result, $registrations, $notification);
        if ($info != null) {
            $url = "https://centor-concours.firebaseio.com/users/" . $info->getUid() . "/registrationsId/.json";
            $data = array($event->getRegistration()->getRegistrationId() => true);
            $this->fcm->sendOrGetData($url, $data, 'PATCH');
        }
    }

    public function onUserCreated(InfoEvent $event)
    {
        $info = $event->getInfo();
        $registrations = $info->getRegistrations();
        $notification = $this->_em->getRepository('MessagerBundle:Notification')->findOneById(580);
        if (is_null($notification))
            return null;
        $notification->setIncludeMail(true);
        $this->_em->flush();
        $registrationIds = $this->sendTo($registrations);
        $data = array('page' => 'notification', 'id' => $notification->getId());
        $result = $this->sendToTokens($registrationIds, $notification, $data);
        $this->controlFake($result, $registrations, $notification);
    }


    public function onCommandeConfirmed(CommandeEvent $event)
    {
        $commande = $event->getCommande();
        $info = $commande->getInfo();
        if ($commande->getStatus() == 'PAID') {
            $notification = new Notification('private');
            if ($commande->getSession() != null) {
                $body = $this->twig->render('MessagerBundle:notification:confirmation_abonnement.html.twig', array('commande' => $commande));
                $notification->setTitre($commande->getSession()->getNomConcours())
                    ->setSousTitre($commande->getSession()->getNomConcours())
                    ->setText($body)
                    ->setSendDate(new \DateTime())
                    ->setIncludeMail(true)
                    ->setSendNow(true);
                $this->_em->persist($notification);
                $this->_em->flush();
                $registrations = $info->getRegistrations();
                $data = array('page' => 'notification', 'id' => $notification->getId());
                $result = $this->sendToTokens($this->sendTo($registrations), $notification, $data);
                $this->controlFake($result, $registrations, $notification);
                $url = "https://centor-concours.firebaseio.com/groupes/" . $commande->getSession()->getId() . "/members/.json";
                $data = array($info->getUid() => array('uid' => $info->getUid(), 'displayName' => $info->getDisplayName(), 'photoURL' => $info->getPhotoURL()));
                $this->fcm->sendOrGetData($url, $data, 'PATCH');
            } elseif ($commande->getRessource() != null) {
                $body = $this->twig->render('MessagerBundle:notification:confirmation_ressource.html.twig', array('commande' => $commande));
                $notification
                    ->setTitre($commande->getRessource()->getNom())
                    ->setSousTitre($commande->getRessource()->getNom())
                    ->setText($body)
                    ->setSendDate(new \DateTime())
                    ->setIncludeMail(true)
                    ->setImageEntity($commande->getRessource()->getFileEntity())
                    ->setSendNow(true);
                $this->_em->persist($notification);
                $this->_em->flush();
                $registrations = $info->getRegistrations();
                $data = array('page' => 'notification', 'id' => $notification->getId());
                $result = $this->sendToTokens($this->sendTo($registrations), $notification, $data);
                $this->controlFake($result, $registrations, $notification);
            }
        }
    }

    public function onFillProfilInvited(InfoEvent $event)
    {
        $destinations = $this->_em->getRepository('AdminBundle:UserAccount')->findNotProfilFilled();
        $tokens = array();
        $batchSize = 30;
        $notification = new Notification('private');
        $notification->setTitre('Completez votre profil')
            ->setSousTitre("Nous voudrions en savoir plus sur vous afin de mieux vous informer des offres concours.")
            ->setSendDate(new \DateTime())
            ->setIncludeMail(false)
            ->setSendNow(true);
        foreach ($destinations as $key => $info) {
            $body = $this->twig->render('AdminBundle:info:profil_invite.html.twig', array('info' => $info));
            $notif = clone $notification;
            $notif->setText($body)->setIncludeMail(true);
            foreach ($info->getRegistrations() as $registration) {
                $tokens[] = $registration->getRegistrationId();
                $sending = new Sending($registration, $notif);
                $this->_em->persist($notif);
                $this->_em->persist($sending);
            }
            if (($key % $batchSize) === 0) {
                $this->_em->flush();
            }
        }
        $this->_em->flush();
        $this->_em->clear();
        $result = $this->sendToTokens($tokens, $notification);
    }


    /**
     * Displays a form to edit an existing notification entity.
     *
     */
    public function sendTo($registrations)
    {
        $registrationIds = array();
        if (empty($registrations))
            return $registrationIds;
        foreach ($registrations as $registration) {
            $registrationIds[] = $registration->getRegistrationId();
        }
        return $registrationIds;
    }


    public function sendToTokens($registrationIds, Notification $notification, $data = array())
    {
        $data = array(
            'registration_ids' => array_values($registrationIds),
            'notification' => array(
                'title' => $notification->getTitre(),
                'body' => $notification->getSousTitre(),
                'badge' => 1,
                'image' => is_null($notification->getImageEntity()) ? '' : $notification->getImageEntity()->getUrl(),
                'sound' => "default",
                'click_action' => 'FCM_PLUGIN_ACTIVITY',
                'tag' => 'message' . $notification->getId()),
            'data' => $data
        );
        return $this->fcm->sendMessage($data);
    }

    public function sendToTopic($topic, Notification $notification, $data = array())
    {
        $data = array(
            'to' => '/topics/' . $topic,
            'notification' => array(
                'title' => $notification->getTitre(),
                'body' => $notification->getSousTitre(),
                'badge' => 1,
                'image' => is_null($notification->getImageEntity()) ? '' : $notification->getImageEntity()->getUrl(),
                'sound' => "default",
                'click_action' => 'FCM_PLUGIN_ACTIVITY',
                'tag' => 'message' . $notification->getId()),
            'data' => $data
        );
        return $this->fcm->sendMessage($data);
    }

    public function sendToAll(Notification $notification, $data = array())
    {
        $data = array(
            'to' => '/topics/centor-public',
            'notification' => array(
                'title' => $notification->getTitre(),
                'body' => $notification->getSousTitre(),
                'badge' => 1,
                'image' => is_null($notification->getImageEntity()) ? '' : $notification->getImageEntity()->getUrl(),
                'sound' => "default",
                'click_action' => 'FCM_PLUGIN_ACTIVITY',
                'tag' => 'message' . $notification->getId()),
            'data' => $data
        );
        return $this->fcm->sendMessage($data);
    }

    public function onMessageEnd(ResultEvent $event)
    {
        $result = $event->getFCMResult();
        $descTokens = $event->getFCMDescsTokens();
        $registrations = $this->_em->getRepository('MessagerBundle:Registration')->findByRegistrationIds($descTokens);
        $this->controlFake($result, $registrations);
        $this->_em->flush();
    }


    public function onSheduleToSend(NotificationEvent $event)
    {
        try {
            $notification = $event->getNotification()
                ->setSendDate(new \DateTime())
                ->setSendNow(true);
            $this->_em->persist($notification);
            foreach ($event->getDescs() as $registration) {
                $sending = new Sending($registration, $notification);
                $this->_em->persist($sending);
            }
            $this->_em->flush();
            $this->sendPushNotification($event, $notification, $event->getData());
        } catch (OptimisticLockException $e) {}
    }


    public function controlFake($result, $registrations, Notification $notification = null)
    {
        if (is_null($result) || !array_key_exists('results', $result))
            return null;
        $resultats = $result['results'];
        $batchSize = 500;
        foreach ($registrations as $key => $registration) {
            if (array_key_exists($key, $resultats))
                if (array_key_exists('error', $resultats[$key]))
                    $registration->setIsFake(true);
                elseif (!is_null($notification) && $notification->getIncludeMail()) {
                    $sending = new Sending($registration, $notification);
                    $this->_em->persist($sending);
                    if (($key % $batchSize) === 0) {
                        $this->_em->flush();
                    }
                }
            $registration->setLastControlDate(new \DateTime());
        }
        $this->_em->flush();
    }

    /**
     * @param NotificationEvent $event
     * @param Notification $notification
     * @param array $data
     * @param $registrations
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function sendPushNotification(NotificationEvent $event, Notification $notification, array $data)
    {
        if (!is_null($event->getTopic())) {
            $this->sendToTopic($event->getTopic(), $notification, $data);
            return;
        } elseif (empty($event->getDescs())) {
            $this->sendToAll($notification, $data);
            return;
        }
        $registrationsGroups = array_chunk($event->getDescs(), 950);
        foreach ($registrationsGroups as $key => $registrations) {
            $tokens = $this->sendTo($registrations);
            $result = $this->sendToTokens($tokens, $notification, $data);
            $this->controlFake($result, $registrations, $notification);
            $this->_em->flush();
        }
    }
}

<?php

namespace App\Listener;

use App\Entity\Post;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class ImageCacheSubscriber implements EventSubscriber
{
    /**
     * @var CacheManager
     */
    private $manager;

    /**
     * @var UploaderHelper
     */
    private $helper;

    public function __construct(CacheManager $manager, UploaderHelper $helper)
    {
        $this->manager = $manager;
        $this->helper = $helper;
    }

    public function getSubscribedEvents()
    {
        return [
            'preRemove',
            'preUpdate'
        ];
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Post){
            return;
        }
        $this->manager->remove($this->helper->asset($entity, 'imageFile'));
    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$entity instanceof Post){
            return;
        }
        if ($entity->getImageFile() instanceof UploadedFile){
            $this->manager->remove($this->helper->asset($entity, 'imageFile'));
        }
    }
}
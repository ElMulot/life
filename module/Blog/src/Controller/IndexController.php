<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Blog\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Main\Form\LocaleForm;
use Blog\Entity\Post;

/**
 * @package		Blog\Controller
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

class IndexController extends AbstractActionController 
{
    public $entityManager;
    
    private $postManager;
    
    private $imageManager;
    
    private $sessionContainer;
    
    private $translator;

    private $config;
    
    public function __construct($entityManager, $postManager, $imageManager, $sessionContainer, $translator, $config) 
    {
        $this->entityManager = $entityManager;
        $this->postManager = $postManager;
        $this->imageManager = $imageManager;
        $this->sessionContainer = $sessionContainer;
        $this->translator = $translator;
        $this->config = $config;
    }
    
    public function indexAction() 
    {
    	$localeForm = new LocaleForm($this->sessionContainer, $this->config);
    	
    	if ($this->getRequest()->isPost())
    	{
    	
    		$data = $this->params()->fromPost();
    		$localeForm->setData($data);
    	
    		if ($localeForm->isValid())
    		{
    			$this->translator->setLocale($data['language']);
    			$this->sessionContainer['locale'] = $data['language'];
    		}
    	}

    	$tagFilter = $this->params()->fromQuery('tag', null);

        if ($tagFilter) {
            // Filter posts by tag or food
            $posts = $this->entityManager->getRepository(Post::class)->findPostsByTag($tagFilter, $this->translator->getLocale());
        }
        else
		{
			// Get recent posts
            $posts = $this->entityManager->getRepository(Post::class)
                    ->findBy(['language' => $this->translator->getLocale(), 'status'=>Post::STATUS_PUBLISHED], 
                             ['dateCreated'=>'DESC']);
        }
        
        // Get popular tags.
        $tagCloud = $this->postManager->getTagCloud();
        
        // Render the view template.
        return new ViewModel([
            'posts' => $posts,
        	'localeForm' => $localeForm,
            'postManager' => $this->postManager,
            'tagCloud' => $tagCloud
        ]);
    }
}

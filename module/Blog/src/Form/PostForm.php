<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace Blog\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Blog\Entity\Post;
use User\Entity\Food;
use Zend\InputFilter\FileInput;

/**
 * @package		Admin\Controller
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

class PostForm extends Form
{
	private $entityManager;
	
	private $translator;
	
	private $config;
	
	private $step = 1;

	private $language = null;
	
	private $foods;

    public function __construct($entityManager, $translator, $config, $step = 1, $language = null)
    {
        parent::__construct('post-form');
		$this->setAttribute('method', 'post');
		$this->entityManager = $entityManager;
		$this->translator = $translator;
		$this->config = $config;
		$this->step = $step;
		
		if ($language == null)
		{
			if ($this->translator->getLocale() == null)
				$this->language = isset($this->config['application_settings']['default_language'])?$this->config['application_settings']['default_language']:'en_US';
			else
				$this->language = $this->translator->getLocale();
		}
		else
		{
			$this->language = $language;
		}
		
		$foods = $this->entityManager->getRepository(Food::class)->findBy(['language' => $this->language], ['foodName' => 'ASC']);
		foreach ($foods as $food) {
			$this->foods[$food->getId()] = $food->getFoodName();
		}
		
		$this->addElements();
		$this->addInputFilter();
    }

    protected function addElements() 
    {
    	if ($this->step == 1)
    	{
    		$this->add([
    				'type' => 'select',
    				'name' => 'language',
    				'options' => [
    						'label' => 'LANGUAGE',
    						'value_options' => $this->config['application_settings']['aviable_languages']
    				],
    				'attributes' => [
    						'value' => $this->language
    				]
    		]);
    		
    		$this->add([
    				'type'  => 'submit',
    				'name' => 'submit',
    				'attributes' => [
    						'value' => 'Suivant',
    				],
    		]);
    		
    		$this->add([
    				'type'  => 'button',
    				'name' => 'cancel',
    				'options' => [
    						'label' => 'Annuler'
    				],
    		]);
    	} else {
    	
	    	//field select only for render
	    	$this->add([
	    			'type' => 'select',
	    			'name' => 'language',
	    			'options' => [
	    					'label' => 'LANGUAGE',
	    					'value_options' => $this->config['application_settings']['aviable_languages']
	    			],
	    			'attributes' => [
	    					'value' => $this->language
	    			]
	    	]);
	    	
	        $this->add([        
	            'type'  => 'text',
	            'name' => 'title',
	            'options' => [
	                'label' => 'Titre',
	            ],
	        ]);
	        
	        $this->add([
	        		'type'  => 'file',
	        		'name' => 'image',
	        		'options' => [
	        				'label' => 'Importer une nouvelle image',
	        		],
	        		'attributes' => [
	        				'id' => 'image',
	        		],
	        ]);
	        
	        $this->add([
	        		'type'  => 'button',
	        		'name' => 'cancel_image',
	        		'options' => [
	        				'label' => 'Supprimer l\'image',
	        		],
	        		'attributes' => [
	        				'onclick' => "$('#thumbnail').hide(); $('#no_image').show(); $('#has_already_image').val(0); $('#image').val('');"
	        		]
	        ]);
	        
	        $this->add([
	        		'type'  => 'hidden',
	        		'name' => 'has_already_image',
	        		'attributes' => [
	        				'id' => 'has_already_image',
	        		],
	        ]);
	        
	        $this->add([
	            'type'  => 'textarea',
	            'name' => 'content',
	            'options' => [
	                'label' => 'Contenu',
	            ],
	        ]);
	        
	        $this->add([
	        		'type' => 'select',
	        		'name' => 'foods',
	        		'options' => [
	        				'label' => 'Lien avec un ou plusieurs aliments',
	        				'value_options' => $this->foods
	        		],
	        		'attributes' => [
	        				'multiple' => true,
	        				'data-actions-box' => true,
	        				'data-live-search' => true
	        		]
	        ]);
	        
	        $this->add([
	        		'type'  => 'text',
	        		'name' => 'tags',
	        		'options' => [
	        				'label' => 'Tags',
	        		],
	        ]);
	        
	        $this->add([
	            'type'  => 'select',
	            'name' => 'status',
	            'options' => [
	                'label' => 'Status',
	                'value_options' => [
	                    Post::STATUS_PUBLISHED => 'Publier',
	                    Post::STATUS_DRAFT => 'Brouillon',
	                ]
	            ],
	        ]);
	        
	        $this->add([
	            'type'  => 'submit',
	            'name' => 'submit',
	            'attributes' => [                
	                'value' => 'Poster',
	            ],
	        ]);
	        
	        $this->add([
	        		'type'  => 'button',
	        		'name' => 'cancel',
	        		'options' => [
	        				'label' => 'Annuler'
	        		],
	        ]);
    	}
    }

    private function addInputFilter() 
    {
        
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
		
        if ($this->step == 1) {
        	$inputFilter->add([
        			'name' => 'language',
        			'required' => true,
        			'filters' => [
        					[
        							'name' => 'StringTrim'
        					]
        			],
        			'validators' => [
        					[
        							'name' => 'InArray',
        							'options' => [
        									'haystack' => array_keys($this->config['application_settings']['aviable_languages'])
        							]
        					]
        			]
        	]);
        } else {
	        $inputFilter->add([
	        		'name' => 'language',
	        		'required' => false,
	        ]);
	        
	        $inputFilter->add([
	                'name'     => 'title',
	                'required' => true,
	                'filters'  => [
	                    ['name' => 'StringTrim'],
	                    ['name' => 'StripTags'],
	                    ['name' => 'StripNewlines'],
	                ],                
	                'validators' => [
	                    [
	                        'name'    => 'StringLength',
	                        'options' => [
											'min' => 1,
											'max' => 1024
									]
							]
					]
			]);
			
			$inputFilter->add([
					'type' => FileInput::class,
					'name' => 'image',
					'required' => false,
					'validators' => [
							[
									'name' => 'FileUploadFile'
							],
							[
									'name' => 'FileIsImage'
							],
							[
									'name' => 'FileImageSize',
									'options' => [
											'minWidth' => 128,
											'minHeight' => 128,
											'maxWidth' => 4096,
											'maxHeight' => 4096
									]
							]
					],
					'filters' => [
							[
									'name' => 'FileRenameUpload',
									'options' => [
											'target'=>'./' . $this->config['application_settings']['image_dir'],
											'useUploadName' => false,
											'useUploadExtension' => false,
											'overwrite' => true,
	        								'randomize'=>true
	        						]
	        				]
	        		],
	        ]);
	        
	        $inputFilter->add([
	                'name'     => 'content',
	                'required' => true,
	                'validators' => [
	                    [
	                        'name'    => 'StringLength',
	                        'options' => [
	                            'min' => 1,
	                            'max' => 4096
	                        ],
	                    ],
	                ],
	            ]);   
	        
	        $inputFilter->add([
	        		'name' => 'foods',
	        		'required' => false,
	        		'filters' => [
	        				['name' => 'StringTrim'],
	        				['name' => 'StripTags'],
	        				['name' => 'StripNewlines'],
	        		],
	        ]);
	        
	        /*
	        $inputFilter->add([
	        		'name' => 'foods',
	        		'required' => false,
	        		'filters' => [
	        				['name' => 'StringTrim'],
	        		],
	        		'validators' => [
	        				[
	        				'name' => 'InArray',
	        				'options' =>
	        				[
	        						'haystack' => array_keys($this->foods),
	        						'recursive' => true,
	        				]
	        				]
	        		],
	        ]);
	        */
	        
	        $inputFilter->add([
	                'name'     => 'tags',
	                'required' => false,
	                'filters'  => [                    
	                    ['name' => 'StringTrim'],
	                    ['name' => 'StripTags'],
	                    ['name' => 'StripNewlines'],
	                ],                
	                'validators' => [
	                    [
	                        'name'    => 'StringLength',
	                        'options' => [
	                            'min' => 1,
	                            'max' => 1024
	                        ],
	                    ],
	                ],
	            ]);
                	
        }
    }
}


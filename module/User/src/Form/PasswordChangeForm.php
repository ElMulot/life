<?php

/**
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @link		http://life.je.gfns.ru/
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */

namespace User\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

/**
 * @package		User\Form
 * @copyright	Copyright (c) 2015 - 2016
 * @license		https://creativecommons.org/licenses/by-nc-sa/4.0/legalcode This work is licensed under a Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License.
 * @author		Mulot
 * @version		0.1 alpha
 * @since		File available since 0.1 alpha
 */
class PasswordChangeForm extends Form
{   
    // There can be two scenarios - 'change' or 'reset'.
    private $scenario;
    
    /**
     * @param string $scenario Either 'change' or 'reset'.     
     */
    public function __construct($scenario)
    {
        parent::__construct('password-change-form');
     
        $this->scenario = $scenario;

        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();          
    }
    
    protected function addElements() 
    {
        // If scenario is 'change', we do not ask for old password.
        if ($this->scenario == 'change') {
        
            $this->add([            
                'type'  => 'password',
                'name' => 'old_password',
                'options' => [
                    'label' => 'USER_OLD_PASSWORD',
                ],
            ]);       
        }
        
        $this->add([            
            'type'  => 'password',
            'name' => 'new_password',
            'options' => [
                'label' => 'USER_NEW_PASSWORD',
            ],
        ]);
        
        $this->add([            
            'type'  => 'password',
            'name' => 'confirm_new_password',
            'options' => [
                'label' => 'USER_CONFIRM_NEW_PASSWORD',
            ],
        ]);
        
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);
        
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [                
                'value' => 'SAVE'
            ],
        ]);
    }
    

    private function addInputFilter() 
    {
        $inputFilter = new InputFilter();        
        $this->setInputFilter($inputFilter);
        
        if ($this->scenario == 'change') {
            
            $inputFilter->add([
                    'name'     => 'old_password',
                    'required' => true,
                    'filters'  => [                    
                    ],                
                    'validators' => [
                        [
                            'name'    => 'StringLength',
                            'options' => [
                                'min' => 4,
                                'max' => 64
                            ],
                        ],
                    ],
                ]);      
        }
        
        $inputFilter->add([
                'name'     => 'new_password',
                'required' => true,
                'filters'  => [                    
                ],                
                'validators' => [
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'min' => 4,
                            'max' => 64
                        ],
                    ],
                ],
            ]);
        
        $inputFilter->add([
                'name'     => 'confirm_new_password',
                'required' => true,
                'filters'  => [                    
                ],                
                'validators' => [
                    [
                        'name'    => 'Identical',
                        'options' => [
                            'token' => 'new_password',                            
                        ],
                    ],
                ],
            ]);
    }
}


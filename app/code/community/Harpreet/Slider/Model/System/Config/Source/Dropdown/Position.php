<?php


class Harpreet_Slider_Model_System_Config_Source_Dropdown_Position
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'top',
                'label' => 'Top',
            ),
            array(
                'value' => 'bottom',
                'label' => 'Bottom',
            ),
            array(
                'value' => 'lefttop',
                'label' => 'Top at Left side',
            ),
            array(
                'value' => 'righttop',
                'label' => 'Top at Right side',
            ),
            array(
                'value' => 'leftbottom',
                'label' => 'Bottom at Left side',
            ),
            array(
                'value' => 'rightbottom',
                'label' => 'Bottom at Right side',
            )
        );
    }
}
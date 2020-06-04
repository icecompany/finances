<?php
use Joomla\CMS\Form\FormRule;
defined('_JEXEC') or die;

class JFormRuleAmount extends FormRule
{
    protected $regex = '^[0-9\.]{1,13}$';
}
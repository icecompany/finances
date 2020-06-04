<?php
use Joomla\CMS\Form\FormRule;
defined('_JEXEC') or die;

class JFormRuleScore extends FormRule
{
    protected $regex = '^[0-9А-Я\/\-\.]{0,20}$';
}
<?php
/**
 * @package Framework
 * @subpackage Textarea
 * @author Jess Green <jgreen@psy-dreamer.com>
 */
class Textarea extends Element
{
        public function  __construct() {
        $args = func_get_args();

        $this->_name = $args['0'];
        $this->setAttributes('name', $this->_name);

    }

    public function __toString()
    {
        $attrArray = array();
        $form_name = $this->getForm_ID();
        foreach($this->_attr as $attr => $value) {
            if ($attr != 'name' && $attr != 'value') {
                $attrArray[] = "$attr=\"$value\"";
            } else {
                $attrArray[] = "$attr=\"{$form_name}[$value]\"";
            }
        }

        $attr = implode(' ', $attrArray);

        $htmlArray = array(
            'open'    => '<p>',
            'content' => '',
            'closing' => '<p>',
        );

        $label = '';
        if (!empty($this->_label)) {
            $id = $this->getAttributes('id');
            $class = " class=\"label-$id\"";
            $label = "<label for=\"$id\"$class>$this->_label</label>\r\n";
        }

        $htmlArray['content'] = $label . "<textarea $attr>%$this->_name%</textarea>\r\n";

        $this->_html = implode(' ', $htmlArray);

        return $this->_html;
    }

}
?>
<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Bloque de saludo al mundo
 *
 * @package   block_updateurl
 * @copyright 2021 IPLACEX
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class block_updateurl extends block_base {

    function init() {
        $this->title = get_string('pluginname', 'block_updateurl');
    }

    public function get_content() {
        global $COURSE, $DB;

        if ($this->content !== null) {
            if($this->config->disabled){
                return null;
            } else {
                return $this->content;
            }
        }
    
        $this->content         =  new stdClass;
        if (!empty($this->config->text)) {
            $this->content->text = $this->config->text;
        } else {
            $this->content->text = '<b>Actualiza o Modifica</b> cualquier tipo de dirección o hipervinculo por otra correspondiente mediante un determinado archivo CSV';
        }

        // Desplegar los registros
        if ($simplehtmlpages = $DB->get_records(
            'block_updateurl')) {
         $this->content->text .= html_writer::start_tag('ul');
            foreach ($simplehtmlpages as $simplehtmlpage) {
                $pageurl = new moodle_url(
                    '/blocks/updateurl/view.php', 
                    array('blockid' => $this->instance->id, 'courseid' => $COURSE->id, 'id' => $simplehtmlpage->id, 'viewpage' => '1')
                );
                $this->content->text .= html_writer::start_tag('li');
                $this->content->text .= html_writer::link($pageurl, $simplehtmlpage->userid);
                $this->content->text .= html_writer::end_tag('li');
            }
            $this->content->text .= html_writer::end_tag('ul');
        }
        else {
            echo('Esto es null');
        }

        // $this->content->text .= html_writer::start_tag('ul');
        //     $this->content->text .= html_writer::start_tag('li');
        //     $this->content->text .= html_writer::link($pageurl, $DB->get_records('block_updateurl'));
        //     $this->content->text .= html_writer::end_tag('li');
        // $this->content->text .= html_writer::end_tag('ul');

        $url = new moodle_url(
            '/blocks/updateurl/view.php', 
            array('blockid' => $this->instance->id, 'courseid' => $COURSE->id)
        );

        $this->content->footer = html_writer::link($url, get_string('addpage', 'block_updateurl'));
     
        return $this->content;
    }

    public function specialization() {
        if (isset($this->config)) {
            if (empty($this->config->title)) {
                $this->title = get_string('defaulttitle', 'block_updateurl');
            } else {
                $this->title = $this->config->title;
            }
     
            if (empty($this->config->text)) {
                $this->config->text = get_string('defaulttext', 'block_updateurl');
            }    
        }
    }

    public function instance_allow_multiple() {
        return true;
    }
    
    function  has_config ()
    {
        return  true ;
    }

    public function instance_config_save($data,$nolongerused =false) {
        global $CFG;
        
        if (!empty($CFG->block_updateurl_allowhtml)) {
            // && $CFG->block_holamundo_allowhtml == '1'
            $data->text = strip_tags($data->text);
        } 
    
        // Implementación predeterminada definida en la clase principal
        return parent::instance_config_save($data,$nolongerused);
    }

    // public function hide_header(){
        // return true;
    // }

    // Permite filtrar acceso al bloque 
    public function applicable_formats() {
        return array(
          'site-index' => true,
          'course-view' => true, 
          'course-view-social' => false,
          'mod' => true, 
          'mod-quiz' => false
        );

        
    }
      
}
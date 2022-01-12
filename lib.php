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
 * Archivo de funciones auxiliares lib.php
 *
 * @package   block_updateurl
 * @copyright 2021 su nombre
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function block_updateurl_images() {
    return array(html_writer::tag('img', '', array('alt' => get_string('red', 'block_updateurl'), 'src' => "pix/red.png")),
                html_writer::tag('img', '', array('alt' => get_string('blue', 'block_updateurl'), 'src' => "pix/blue.png")),
                html_writer::tag('img', '', array('alt' => get_string('green', 'block_updateurl'), 'src' => "pix/green.png")));
}


// function csvtoarray($archivo,$delimitador = ","){

//     if(!empty($archivo) && !empty($delimitador) && is_file($archivo)):

//         $array_total = array();
//         $fp = fopen($archivo,"r");

//         // Se procesa la data y esta se comprime dentro de un arreglo
//         while ($data = fgetcsv($fp, $readcount, $delimitador)){
//             $num = count($data);
//             $array_total[] = array_map($fromform->encoding,$data);
//         }
//         fclose($fp);
//         return $array_total;

//     else:
//         return false;
//     endif;
// }

function csvToArray($fileOpen, $delimitador = ",", $totalRaw, $encoding)
{
    if (!empty($fileOpen) && !empty($delimitador) && is_file($archivo)) 
    {
        
        $total_array = array();
        $fp = fopen($archivo, "r");

        // Se procesa la data y esta se comprime dentro de un arreglo
        while($data = fgetcsv($fp, $totalRaw, $delimitador)){
            $array_total[] = array_map($encoding, $data);
        }
        fclose($fp);
        return $array_total;
    }else
    {
        return false;
    }
}
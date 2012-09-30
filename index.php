<?php


class NagiosStatusParser {

    private $data = array();
    private $lines = array();


    public function __construct($status_filename) {

        if (file_exists($status_filename)) {

            $this->lines = file($status_filename);
        }
        else {

            exit('Unable to read the status file.');
        }
    }


    public function execute() {

        foreach ($this->lines as $line) {

            if (($p = strpos($line, '{')) !== false) {

                $section = trim(substr($line, 0, $p));
                $values = array();
                $hostname = '';

                if ($section !== 'hoststatus' && $section !== 'servicestatus') {

                    $section == '';
                }
            }
            else if (($p = strpos($line, '=')) !== false) {

                $key = trim(substr($line, 0, $p));
                $value = trim(substr($line, $p + 1));

                $values[$key] = $value;

                if ($key === 'host_name') {

                    $hostname = $value;
                }
            }
            else if (($p = strpos($line, '}')) !== false) {

                if ($hostname && $section) {

                    if (! isset($this->data[$hostname])) {

                        $this->data[$hostname] = array();
                    }

                    if (! isset($this->data[$hostname][$section])) {

                        $this->data[$hostname][$section] = array();
                    }

                    $this->data[$hostname][$section][] = $values;
                }

                $section = '';
                $hostname = '';
                $values = array();
            }

        }

        return $this->data;
    }
}


$p = new NagiosStatusParser('/var/nagios/status.dat');

echo json_encode($p->execute());

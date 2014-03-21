<?php

class Grapher {

    private $socket;

    function __construct($graphite_server, $graphite_port, $base_ns) {
        $this->base_ns = $base_ns;
        $this->graphite_server = $graphite_server;
        $this->graphite_port = $graphite_port;
    }

    public function graphResults($results) {
        foreach ($results as $result) {
            $this->graph($result['location'], $result['label'], $result['date'], $result['data']);
        }
    }

    public function graph($location, $label, $date, $data) {
        $base_ns = $this->base_ns;
        foreach($data as $key => $value) {
            echo "$base_ns.$label.$key $value $date\n";

            try {
                if (is_null($this->socket)) {
                    $this->socket = fsockopen("udp://" . $this->graphite_server, $this->graphite_port);
                }
                if (!$this->socket) {
                    return;
                }
                @fwrite($this->socket, "$base_ns.$label.$key:$value|g");
            } catch (\Exception $e) {}

        }
    }
}

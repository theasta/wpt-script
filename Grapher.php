<?php

class Grapher {

    private $socket;

    function __construct($server, $port, $base_ns, $service = 'graphite') {
        $this->base_ns = $base_ns;
        $this->server = $server;
        $this->port = $port;
        $this->service = $service;
    }

    public function graphResults($results) {
        var_dump($results);
        foreach ($results as $result) {
            $this->graph($result['label'], $result['date'], $result['data']);
        }
    }

    public function graph($label, $date, $data) {
        $base_ns = $this->base_ns;
        foreach($data as $key => $value) {
            echo "$base_ns.$label.$key $value $date\n";
            if ($this->service === 'graphite'){
                `echo "$base_ns.$label.$key $value $date" | nc $this->server $this->port`;
            } else {
                try {
                    if (is_null($this->socket)) {
                        $this->socket = fsockopen("udp://" . $this->server, $this->port);
                    }
                    if (!$this->socket) {
                        return;
                    }
                    @fwrite($this->socket, "$base_ns.$label.$key:$value|g");
                } catch (\Exception $e) {}
            }

        }
    }
}

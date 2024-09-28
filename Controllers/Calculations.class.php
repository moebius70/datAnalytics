<?php

class Calculations extends Controller {
    private $p;
    private $r;
    private $t;
    private $ci;
    private $ar;

    public function __construct($p, $r, $t) {
        $this->p = (float)$p;
        $this->r = (float)$r;
        $this->t = (int)$t;
    }

    // Compound ROI
    // FV = PV (1 + r)^n
    public function compoundroi() {
        $this->ci = round($this->p * pow((1 + $this->r), $this->t), 2);
        return $this->ci;
    }
    
    // Average interest 
    // r = (FV / PV)^(1/n) - 1
    public function roi() {
        // Ensure compoundroi has been called to set ci
        if ($this->ci === null) {
            $this->compoundroi();
        }
        $this->ar = (pow(($this->ci / $this->p), (1 / $this->t)) - 1) * 100;
        return $this->ar;
    }

}

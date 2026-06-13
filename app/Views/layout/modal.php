<?php
if (isset($modal)) {
    for ($i = 0; $i < count($modal); $i++) {
        echo $this->include("./modal/$modal[$i]");
    }
}

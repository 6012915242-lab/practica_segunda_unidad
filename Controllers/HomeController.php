<?php

class HomeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /** /home */
    public function index(): void
    {
        $data["page_title"] = "Inicio";
        $this->views->render($this, "index", $data);
    }
}

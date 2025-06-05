<?php

namespace App\View\Components\Feedback;

use Illuminate\View\Component;

class FeedbackPrompt extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.feedback-prompt');
    }
}

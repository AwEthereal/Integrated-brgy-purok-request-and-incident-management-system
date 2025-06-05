<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FeedbackForm extends Component
{
    public $type;
    public $itemId;
    public $hasFeedback;

    /**
     * Create a new component instance.
     *
     * @param string $type Type of item (request or incident)
     * @param int $itemId ID of the request or incident
     * @param bool $hasFeedback Whether feedback has already been submitted
     * @return void
     */
    public function __construct($type, $itemId, $hasFeedback = false)
    {
        $this->type = $type;
        $this->itemId = $itemId;
        $this->hasFeedback = $hasFeedback;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.feedback-form', [
            'type' => $this->type,
            'itemId' => $this->itemId,
            'hasFeedback' => $this->hasFeedback
        ]);
    }
}

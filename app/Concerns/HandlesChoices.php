<?php

namespace App\Concerns;

trait HandlesChoices
{
    /**
     * Ask for app selection(s) and return the resolved configs.
     *
     * @param  string  $question
     * @param  array  $options
     * @param  bool  $allowMultiple
     * @return string|array
     */
    protected function buildQuestion(string $question, array $options, bool $allowMultiple = false)
    {
        $choices = ['none' => 'None'];

        foreach ($options as $key => $value) {
            $choices[$key] = $value;
        }

        $choices = array_unique($choices);

        return $this->choice(
            $question,
            $choices,
            'none', null, $allowMultiple,
        );
    }

    /**
     * Build options for apps question.
     *
     * @param  array  $options
     * @return array
     */
    protected function buildOptions(array $options = []): array
    {
        $items = ['none' => 'None'];

        foreach ($options as $key => $option) {
            $items[$key] = "{$option['name']} ({$option['url']})";
        }

        return $items;
    }
}

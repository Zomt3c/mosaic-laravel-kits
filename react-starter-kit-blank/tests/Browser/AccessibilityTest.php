<?php

declare(strict_types=1);

/*
 * Optional: checks a page with axe (labels, contrast, names of buttons and links). Remove the skip
 * to run it, and add more pages the same way.
 *
 * Pages that fade in report too little contrast, because axe measures during the animation, so
 * wait first.
 */

test('the welcome page has no accessibility issues', function () {
    visit('/')->wait(1)->assertNoAccessibilityIssues();
})->skip('Optional, remove this skip to check the page with axe');

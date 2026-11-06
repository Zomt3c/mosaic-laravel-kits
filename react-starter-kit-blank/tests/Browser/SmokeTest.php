<?php

declare(strict_types=1);

it('loads the start page in a real browser', function () {
    visit('/')->assertNoSmoke();
});

// ================================================================
// validate.js   —   simple client-side checks before a form submits
// ================================================================
// On every form with required fields we:
//   1. check each required field has some text in it
//   2. if any are empty, stop the submit and show a friendly
//      message listing them, then put the cursor on the first one
//
// This runs in the BROWSER (client side, JavaScript) so the user
// gets a quick answer without a page reload. The PHP on the server
// still checks everything again — this file is just a nicer
// experience, not a security measure.
// ================================================================

// Look at every <form> on the page and add a submit check to it.
document.querySelectorAll('form').forEach(function (form) {

    // Switch off the browser's own required-field popup. Because the
    // form fields have required="required", the browser would normally
    // block the submit with its own tooltip BEFORE our check below can
    // run. Turning it off here means our message below is the one shown.
    form.noValidate = true;

    form.addEventListener('submit', function (event) {
        var missing = [];   // list of fields that are empty

        // Find every field marked required and see if it is blank.
        form.querySelectorAll('[required]').forEach(function (field) {
            if (field.value.trim() === '') {
                missing.push(field);
            }
        });

        // All fields filled in - let the form submit normally.
        if (missing.length === 0) {
            return;
        }

        // Stop the submit so we can tell the user what is missing.
        event.preventDefault();

        // Work out a readable name for each empty field, using the
        // <label> that belongs to it (e.g. "Username").
        var names = missing.map(function (field) {
            var label = form.querySelector('label[for="' + field.id + '"]');
            return label ? label.textContent.trim() : field.name;
        });

        // Show one message at the top of the form listing the gaps.
        var msg = form.querySelector('.js-message');
        if (!msg) {
            msg = document.createElement('p');
            msg.className = 'js-message';
            form.prepend(msg);   // put it above the fields
        }
        msg.textContent = 'Please fill in: ' + names.join(', ') + '.';

        // Move the cursor to the first empty field.
        missing[0].focus();
    });
});
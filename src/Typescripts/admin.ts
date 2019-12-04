document.addEventListener('DOMContentLoaded', function () {
  adminForm.init();
});

class AdminForm {
  init = () => {
    this.initFormSubmitButton();
  }

  initFormSubmitButton = () => {
    // Enable submit form in action bar
    var submitButtons = document.querySelectorAll("button[data-form]");
    for (var i = 0; i < submitButtons.length; i++) {
      var button = submitButtons[i];
      button.addEventListener('click', function () {
        var form = <HTMLFormElement>document.getElementById(this.getAttribute('data-form'));
        if (form) {
          if (form.reportValidity() || form.checkValidity()) {
            form.submit();
          }
          else {
            console.log('Form invalid');
          }
        }
        else {
          console.log('Form "' + this.getAttribute('data-form') + '" not found!');
        }
      });
    }
  }
}

let adminForm = new AdminForm();
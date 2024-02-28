(function () {
  const formChangePass = document.querySelector('#formChangePassword');

  // Form validation for Change password
  if (formChangePass) {
    const fv = FormValidation.formValidation(formChangePass, {
      fields: {
        oldPassword: {
          validators: {
            notEmpty: {
              message: 'Please enter your old password'
            }
          }
        },
        newPassword: {
          validators: {
            notEmpty: {
              message: 'Please enter new password'
            },
            stringLength: {
              min: 6,
              message: 'Password must be at least 6 characters long'
            }
          }
        },
        confirmPassword: {
          validators: {
            notEmpty: {
              message: 'Please confirm your new password'
            },
            identical: {
              compare: function () {
                return formChangePass.querySelector('[name="newPassword"]').value;
              },
              message: 'Passwords do not match'
            },
            stringLength: {
              min: 6,
              message: 'Password must be at least 6 characters long'
            }
          }
        }
      },
      plugins: {
        trigger: new FormValidation.plugins.Trigger(),
        bootstrap5: new FormValidation.plugins.Bootstrap5({
          eleValidClass: '',
          rowSelector: '.form-password-toggle'
        }),
        autoFocus: new FormValidation.plugins.AutoFocus()
      },
      init: instance => {
        instance.on('plugins.message.placed', function (e) {
          if (e.element.parentElement.classList.contains('input-group')) {
            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
          }
        });
      }
    });
  }
})();

function showSweetAlert(response) {
  Swal.fire({
      icon: response.success ? 'success' : 'error',
      title: response.title,   
      text: response.message,
  });
}
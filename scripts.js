// Redirect to the form page
function redirectToForm() {
  window.location.href = "form.html";
}

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('participationForm');

  form.addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission behavior

    let isValid = true;

    // Clear previous error messages
    document.querySelectorAll('.error-message').forEach(error => {
      error.textContent = '';
    });

    // Validate required fields
    const fields = [
      { id: 'associationName', errorId: 'associationNameError', message: 'يرجى إدخال اسم الجمعية.' },
      { id: 'address', errorId: 'addressError', message: 'يرجى إدخال العنوان.' },
      { id: 'phone', errorId: 'phoneError', message: 'يرجى إدخال رقم الهاتف.' },
      { id: 'fax', errorId: 'faxError', message: 'يرجى إدخال رقم الفاكس.' },
      { id: 'email', errorId: 'emailError', message: 'يرجى إدخال بريد إلكتروني صحيح.' },
      { id: 'facebook', errorId: 'facebookError', message: 'يرجى إدخال رابط صفحة الفايسبوك.' },
      { id: 'approvalDoc', errorId: 'approvalDocError', message: 'يرجى تحميل قرار الإعتماد بصيغة PDF.' },
      { id: 'fieldsOfActivity', errorId: 'fieldsOfActivityError', message: 'يرجى إدخال ميادين نشاط الجمعية.' },
      { id: 'widowsOrphans', errorId: 'widowsOrphansError', message: 'يرجى إدخال موجز تعريفي لقسم التكفل بالأرامل والايتام.' },
     // { id: 'declarantName', errorId: 'declarantNameError', message: 'يرجى إدخال اسم المصرح.' },
    ];

    fields.forEach(({ id, errorId, message }) => {
      const input = document.getElementById(id);
      const errorElement = document.getElementById(errorId);

      if (!input.value.trim()) {
        isValid = false;
        errorElement.textContent = message;
        errorElement.style.display = 'block'; // Ensure the error message is visible
      } else {
        errorElement.style.display = 'none'; // Hide the error message if the input is valid
      }
    });

    // Validate checkbox
    const agreeCheckbox = document.getElementById('agreeRules');
    const agreeRulesError = document.getElementById('agreeRulesError');
    if (!agreeCheckbox.checked) {
      isValid = false;
      agreeRulesError.textContent = 'يجب الموافقة على النظام الداخلي.';
    }

    // Prevent form submission if invalid
    if (!isValid) {
      return;
    }

    // If valid, log the form data (or handle it as needed)
    console.log('Form submitted successfully!');
    alert('تم إرسال الاستمارة بنجاح!');
  });
});
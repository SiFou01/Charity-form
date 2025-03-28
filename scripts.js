// Redirect to the form page
function redirectToForm() {
  window.location.href = "form.html";
}

document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('participationForm');

  form.addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission behavior

    let isValid = true;
    let firstInvalidElement = null; // Track the first invalid input

    // Clear previous error messages
    document.querySelectorAll('.error-message').forEach(error => {
      error.textContent = '';
      error.style.display = 'none'; // Hide all error messages initially
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
      { id: 'declarantName', errorId: 'declarantNameError', message: 'يرجى إدخال اسم المعلن.' },
    ];

    fields.forEach(({ id, errorId, message }) => {
      const input = document.getElementById(id);
      const errorElement = document.getElementById(errorId);

      if (!input.value.trim()) {
        isValid = false;
        errorElement.textContent = message;
        errorElement.style.display = 'block'; // Ensure the error message is visible

        // Track the first invalid input
        if (!firstInvalidElement) {
          firstInvalidElement = input;
        }
      } else if (id === 'phone' || id === 'fax') {
        // Additional validation for phone and fax
        if (!/^\d{10}$/.test(input.value)) {
          isValid = false;
          errorElement.textContent = 'يرجى إدخال رقم مكون من 10 أرقام فقط.';
          errorElement.style.display = 'block';

          // Track the first invalid input
          if (!firstInvalidElement) {
            firstInvalidElement = input;
          }
        } else {
          errorElement.style.display = 'none'; // Hide the error message if valid
        }
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
      agreeRulesError.style.display = 'block';

      // Track the first invalid input
      if (!firstInvalidElement) {
        firstInvalidElement = agreeCheckbox;
      }
    }

    // Validate table rows
    const tableRows = document.querySelectorAll('#boardTable tbody tr');
    const boardTableError = document.getElementById('boardTableError');
    let filledRows = 0;

    tableRows.forEach(row => {
      const inputs = row.querySelectorAll('input');
      const isRowFilled = Array.from(inputs).every(input => input.value.trim() !== '');
      if (isRowFilled) {
        filledRows++;
      }
    });

    if (filledRows < 7) {
      isValid = false;
      boardTableError.textContent = 'يرجى ملء سبعة صفوف على الأقل في الجدول.';
      boardTableError.style.display = 'block';

      // Track the first invalid input
      if (!firstInvalidElement) {
        firstInvalidElement = document.getElementById('boardTable');
      }
    } else {
      boardTableError.style.display = 'none';
    }

    // Scroll to the first invalid input and focus on it
    if (!isValid && firstInvalidElement) {
      firstInvalidElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstInvalidElement.focus();
      return;
    }

    // Collect and log form data
    const formData = {};
    fields.forEach(({ id }) => {
      const input = document.getElementById(id);
      formData[id] = input.value.trim();
    });

    // Collect table data
    formData.boardMembers = [];
    tableRows.forEach(row => {
      const inputs = row.querySelectorAll('input');
      const rowData = Array.from(inputs).map(input => input.value.trim());
      if (rowData.every(value => value !== '')) {
        formData.boardMembers.push(rowData);
      }
    });

    console.log('Form Data:', formData);

    // Simulate successful submission (replace this with your actual email-sending logic)
    fetch('send_email.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(formData),
    })
      .then(response => {
        if (response.ok) {
          // Redirect to the Thank You page
          window.location.href = 'thank-you.html';
        } else {
          alert('حدث خطأ أثناء إرسال الاستمارة. يرجى المحاولة مرة أخرى.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إرسال الاستمارة. يرجى المحاولة مرة أخرى.');
      });
  });
});
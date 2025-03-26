function redirectToForm() {
  window.location.href = "form.html"; // Redirect to the form page
}

function addRow() {
  const table = document.getElementById('boardTable').getElementsByTagName('tbody')[0];
  const newRow = document.createElement('tr');

  newRow.innerHTML = `
    <td><input type="text" name="boardName[]" required pattern="[^\d]+" title="يرجى إدخال نص فقط" /></td>
    <td><input type="text" name="boardJob[]" required pattern="[^\d]+" title="يرجى إدخال نص فقط" /></td>
    <td><input type="text" name="boardRole[]" required pattern="[^\d]+" title="يرجى إدخال نص فقط" /></td>
  `;

  table.appendChild(newRow);
}

// Enable the submit button only when the checkbox is checked
const agreeCheckbox = document.getElementById('agreeRules');
const submitButton = document.getElementById('submitButton');
const participationForm = document.getElementById('participationForm');

if (agreeCheckbox && submitButton) {
  agreeCheckbox.addEventListener('change', function () {
    submitButton.disabled = !this.checked; // Enable/disable the submit button based on checkbox state
    const errorElement = document.getElementById('agreeRulesError');
    if (this.checked) {
      errorElement.style.display = 'none'; // Hide error message when checked
    }
  });
}

// Prevent form submission if the checkbox is not checked
if (participationForm) {
  participationForm.addEventListener('submit', function (event) {
    const errorElement = document.getElementById('agreeRulesError');
    if (!agreeCheckbox.checked) {
      event.preventDefault(); // Prevent form submission
      errorElement.textContent = 'يرجى الموافقة على النظام الداخلي قبل إرسال الاستمارة.'; // Show error message
      errorElement.style.display = 'block';
      agreeCheckbox.focus(); // Focus on the checkbox
    }
  });
}

// Add error message handling for the participation form
if (participationForm) {
  participationForm.addEventListener('submit', function (event) {
    let isValid = true;
    let firstErrorElement = null;

    const fields = [
      { id: 'associationName', errorId: 'associationNameError', message: 'يرجى إدخال اسم الجمعية.' },
      { id: 'address', errorId: 'addressError', message: 'يرجى إدخال العنوان.' },
      { id: 'phone', errorId: 'phoneError', message: 'يرجى إدخال رقم الهاتف بشكل صحيح.' },
      { id: 'fax', errorId: 'faxError', message: 'يرجى إدخال رقم الفاكس بشكل صحيح.' },
      { id: 'website', errorId: 'websiteError', message: 'يرجى إدخال رابط الموقع الإلكتروني بشكل صحيح.' },
      { id: 'email', errorId: 'emailError', message: 'يرجى إدخال البريد الإلكتروني بشكل صحيح.' },
      { id: 'facebook', errorId: 'facebookError', message: 'يرجى إدخال رابط صفحة الفايسبوك بشكل صحيح.' },
      { id: 'approvalDoc', errorId: 'approvalDocError', message: 'يرجى تحميل قرار الإعتماد بصيغة PDF.' },
      { id: 'fieldsOfActivity', errorId: 'fieldsOfActivityError', message: 'يرجى إدخال ميادين نشاط الجمعية.' },
      { id: 'widowsOrphans', errorId: 'widowsOrphansError', message: 'يرجى إدخال موجز تعريفي لقسم التكفل بالأرامل والايتام.' },
    ];

    fields.forEach(({ id, errorId, message }) => {
      const field = document.getElementById(id);
      const errorElement = document.getElementById(errorId);

      if (field) {
        if (id === 'approvalDoc') {
          // Special validation for file input
          if (!field.files || field.files.length === 0) {
            isValid = false;
            errorElement.textContent = message;
            errorElement.style.display = 'block';
            if (!firstErrorElement) {
              firstErrorElement = field;
            }
          } else {
            errorElement.style.display = 'none';
          }
        } else if (!field.value.trim()) {
          // Check if the field is empty
          isValid = false;
          errorElement.textContent = message;
          errorElement.style.display = 'block';
          if (!firstErrorElement) {
            firstErrorElement = field; // Capture the first field with an error
          }
        } else if (!field.checkValidity()) {
          // Check for other validation errors
          isValid = false;
          errorElement.textContent = message;
          errorElement.style.display = 'block';
          if (!firstErrorElement) {
            firstErrorElement = field;
          }
        } else {
          errorElement.style.display = 'none';
        }
      }
    });

    // Validate the table
    const tableRows = document.querySelectorAll('#boardTable tbody tr');
    const boardTableError = document.getElementById('boardTableError');
    const filledRows = Array.from(tableRows).filter(row => {
      const inputs = row.querySelectorAll('input');
      return Array.from(inputs).every(input => input.value.trim() !== '');
    });

    if (filledRows.length < 7) {
      isValid = false;
      boardTableError.textContent = 'يرجى ملء سبعة صفوف على الأقل في الجدول.';
      boardTableError.style.display = 'block';
      if (!firstErrorElement) {
        firstErrorElement = document.getElementById('boardTable');
      }
    } else {
      boardTableError.style.display = 'none';
    }

    // Validate declarant name input
    const declarantName = document.getElementById('declarantName');
    if (declarantName && !declarantName.value.trim()) {
      isValid = false;
      declarantName.style.borderColor = 'red'; // Highlight border in red
      if (!firstErrorElement) {
        firstErrorElement = declarantName;
      }
    } else if (declarantName) {
      declarantName.style.borderColor = ''; // Reset border color
    }

    if (!isValid) {
      event.preventDefault(); // Prevent form submission if there are errors
      if (firstErrorElement) {
        firstErrorElement.scrollIntoView({ behavior: 'smooth', block: 'center' }); // Scroll to the first error
        firstErrorElement.focus(); // Focus on the first error field
      }
    }
  });

  // Reset border color on input
  const declarantName = document.getElementById('declarantName');
  if (declarantName) {
    declarantName.addEventListener('input', function () {
      if (this.value.trim()) {
        this.style.borderColor = ''; // Reset border color when valid
      }
    });
  }
}

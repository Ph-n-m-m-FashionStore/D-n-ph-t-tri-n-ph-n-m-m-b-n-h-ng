<div class="password-requirements">
    <ul id="pw-req-list">
        <li id="pw-length"><i class="fa fa-check"></i> Ít nhất 8 ký tự</li>
        <li id="pw-case"><i class="fa fa-check"></i> Có chữ hoa và chữ thường</li>
        <li id="pw-special"><i class="fa fa-check"></i> Có số hoặc ký tự đặc biệt</li>
    </ul>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var pwInput = document.querySelectorAll('input[type="password"][name="password"]');
    pwInput.forEach(function(input) {
        input.addEventListener('input', function() {
            var val = input.value;
            var container = input.closest('form');
            if (!container) return;
            var pwLength = container.querySelector('#pw-length');
            var pwCase = container.querySelector('#pw-case');
            var pwSpecial = container.querySelector('#pw-special');
            if (!pwLength || !pwCase || !pwSpecial) return;
            if (val.length >= 8) {
                pwLength.style.color = '#28a745';
                pwLength.querySelector('i').className = 'fa fa-check';
            } else {
                pwLength.style.color = 'red';
                pwLength.querySelector('i').className = 'fa fa-times';
            }
            if (/[a-z]/.test(val) && /[A-Z]/.test(val)) {
                pwCase.style.color = '#28a745';
                pwCase.querySelector('i').className = 'fa fa-check';
            } else {
                pwCase.style.color = 'red';
                pwCase.querySelector('i').className = 'fa fa-times';
            }
            if (/[0-9!@#$%^&*(),.?":{}|<>]/.test(val)) {
                pwSpecial.style.color = '#28a745';
                pwSpecial.querySelector('i').className = 'fa fa-check';
            } else {
                pwSpecial.style.color = 'red';
                pwSpecial.querySelector('i').className = 'fa fa-times';
            }
            if (
                val.length < 8 ||
                !(/[a-z]/.test(val) && /[A-Z]/.test(val)) ||
                !(/[0-9!@#$%^&*(),.?":{}|<>]/.test(val))
            ) {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
            }
        });
    });
});
</script>

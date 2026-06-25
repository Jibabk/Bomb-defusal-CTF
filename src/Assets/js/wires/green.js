document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('login-form');
  const username = document.getElementById('username');
  const password = document.getElementById('password');
  const warning = document.getElementById('filter-warning');

  if (!form || !username || !password) {
    return;
  }

  const blockedPatterns = [
    /'/,
    /--/,
    /;/,
    /\/\*/,
    /\*\//,
    /#/,
    /\bOR\b/i,
    /\bAND\b/i,
    /\bUNION\b/i,
    /\bSELECT\b/i,
    /\bINSERT\b/i,
    /\bUPDATE\b/i,
    /\bDELETE\b/i,
    /\bDROP\b/i,
    /\bWHERE\b/i,
  ];

  function hasBlockedToken(value) {
    return blockedPatterns.some((pattern) => pattern.test(value));
  }

  function setWarningVisible(isVisible) {
    if (warning) {
      warning.hidden = !isVisible;
    }
  }

  [username, password].forEach((input) => {
    input.addEventListener('input', () => {
      setWarningVisible(false);
    });
  });

  form.addEventListener('submit', (event) => {
    if (hasBlockedToken(username.value) || hasBlockedToken(password.value)) {
      event.preventDefault();
      setWarningVisible(true);
    }
  });
});

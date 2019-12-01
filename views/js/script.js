var GLOBALS = [];
function hashThenSend(plaintext) {
    if (plaintext !== null && plaintext !== undefined && plaintext !== "")
        document.getElementById('password').value = md5(plaintext);
}
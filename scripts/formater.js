function formatPhoneNumber(phoneNumber) {

    const formattedNumber = phoneNumber.replace(/[^\d+]/g, '');


    if (formattedNumber.startsWith('+49') || isGermanMobileNumber(formattedNumber)) {
        return formattedNumber;
    } else {
        return null;
    }
}


function isGermanMobileNumber(phoneNumber) {

    const germanMobilePattern = /^(?:\+?49)?(?:0)?(?:1[5-7]|1[016789][0-9])[\d]{8}$/;
    return germanMobilePattern.test(phoneNumber);
}

document.addEventListener("input", (event) => {
    const input = event.target;

    if (
        !(input instanceof HTMLInputElement) ||
        !input.matches("[data-phone-input]")
    ) {
        return;
    }

    const rawValue = input.value.replace(/[^0-9+]/g, "");
    const sanitizedValue = rawValue.startsWith("+")
        ? `+${rawValue.slice(1).replace(/\+/g, "")}`
        : rawValue.replace(/\+/g, "");

    if (input.value !== sanitizedValue) {
        input.value = sanitizedValue.slice(0, 20);
    }
});

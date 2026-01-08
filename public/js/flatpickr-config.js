// Mongolian locale
const MongolianLocale = {
    firstDayOfWeek: 1,
    weekdays: {
        shorthand: ["Ня", "Да", "Мя", "Лх", "Пү", "Ба", "Бя"],
        longhand: [
            "Ням",
            "Даваа",
            "Мягмар",
            "Лхагва",
            "Пүрэв",
            "Баасан",
            "Бямба",
        ],
    },
    months: {
        shorthand: [
            "1-р",
            "2-р",
            "3-р",
            "4-р",
            "5-р",
            "6-р",
            "7-р",
            "8-р",
            "9-р",
            "10-р",
            "11-р",
            "12-р",
        ],
        longhand: [
            "Нэгдүгээр сар",
            "Хоёрдугаар сар",
            "Гуравдугаар сар",
            "Дөрөвдүгээр сар",
            "Тавдугаар сар",
            "Зургадугаар сар",
            "Долдугаар сар",
            "Наймдугаар сар",
            "Есдүгээр сар",
            "Аравдугаар сар",
            "Арван нэгдүгээр сар",
            "Арван хоёрдугаар сар",
        ],
    },
    rangeSeparator: " - ",
    weekAbbreviation: "7 хоног",
    scrollTitle: "Томруулахын тулд гүйлгэнэ үү",
    toggleTitle: "Товшиж өөрчлөх",
};

// Global default config
window.flatpickrConfig = {
    datetime: {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        time_24hr: true,
        locale: MongolianLocale,
    },
    date: {
        dateFormat: "Y-m-d",
        locale: MongolianLocale,
    },
    month: {
        dateFormat: "Y-m",
        locale: MongolianLocale,
    },
    time: {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        locale: MongolianLocale,
    },
};

// Helper function
window.initFlatpickr = function (selector, type = "datetime") {
    // flatpickr ачаалагдсан эсэхийг шалгах
    if (typeof flatpickr === "undefined") {
        console.error("Flatpickr library ачаалагдаагүй байна!");
        return null;
    }

    const config = window.flatpickrConfig[type];
    if (!config) {
        console.error("Буруу төрөл:", type);
        return null;
    }

    return flatpickr(selector, config);
};

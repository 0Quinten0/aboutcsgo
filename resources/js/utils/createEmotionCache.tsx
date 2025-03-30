// ./utils/createEmotionCache.js or .tsx
import createCache from "@emotion/cache";

const isBrowser = typeof document !== "undefined";

// On the client side, Create a meta tag at the very top of the head and set it as insertionPoint.
// This assures that MUI styles are loaded first.
// It allows developers to easily override MUI styles with other styling solutions, like CSS solutions.
let insertionPoint;

if (isBrowser) {
    const emotionInsertionPoint = document.createElement("meta");
    emotionInsertionPoint.setAttribute("name", "emotion-insertion-point");
    const head = document.head;
    head.insertBefore(emotionInsertionPoint, head.firstChild);
    insertionPoint = emotionInsertionPoint;
}

const createEmotionCache = () => {
    return createCache({ key: "css", insertionPoint }); // Ensure 'css' is consistent
};

export default createEmotionCache;

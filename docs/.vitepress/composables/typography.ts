import { Ref } from 'vue';

export const useTitle = (el: Ref<HTMLElement>) => {
    const text = el.value.textContent;
    const words = text.trim().split(' ');

    if (words.length > 1) {
        const lastWord = words.pop();
        el.value.innerHTML = words.join(' ') + " <span class='fiov-title-highlight'>" + lastWord + '</span>';
    }
};

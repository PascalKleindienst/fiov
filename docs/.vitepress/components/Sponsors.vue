<template>
    <div class="sponsors">
        <h3>Sponsors</h3>
        <ul>
            <li>
                <a class="sponsor" href="https://laracasts.com/referral/PascalKleindienst" rel="noopener" target="_blank" title="Learn Laravel">
                    <img alt="Laracasts" src="/sponsors/laracast.svg" />
                </a>
            </li>
        </ul>
    </div>
</template>

<style lang="postcss" scoped>
.sponsors {
    margin-top: 1rem;
    border-left: 1px solid var(--vp-c-divider);
    padding-left: 16px;
}

h3 {
    line-height: 32px;
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.sponsor {
    display: block;
    text-align: center;
    margin: 0 auto;
    border: 1px solid var(--vp-c-border);
    transition: all 0.2s ease;
    padding: 1rem 0.75rem;
    border-radius: 4px;
    background: rgba(0, 0, 0, 0.05);

    &:hover {
        border-color: var(--vp-c-brand-1);
        background: rgba(0, 0, 0, 0.1);
    }
}

.dark {
    .sponsor {
        background: rgba(0, 0, 0, 0.8);

        img {
            filter: brightness(0) invert(1);
        }

        &:hover {
            border-color: var(--vp-c-brand-1);
            background: rgba(255, 255, 255, 0.1);
        }
    }
}
</style>

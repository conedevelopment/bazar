import Dispatcher from './../../resources/js/Support/Dispatcher.js';

const dispatcher = new Dispatcher();

test('it listens to events', () => {
    let event = null;

    dispatcher.addEventListener('test', () => event = 'called');

    expect(event).toBeNull();
    dispatcher.dispatchEvent('test');
    expect(event).toBe('called');
});

test('it listens to events once', () => {
    let event = 0;

    dispatcher.addEventListener('test', () => event++, { once: true });

    expect(event).toBe(0);
    dispatcher.dispatchEvent('test');
    expect(event).toBe(1);
    dispatcher.dispatchEvent('test');
    expect(event).toBe(1);
});

test('it removes event listeners', () => {
    let event = 0;
    const fn = () => event++;

    dispatcher.addEventListener('test', fn);
    expect(event).toBe(0);
    dispatcher.dispatchEvent('test');
    expect(event).toBe(1);
    dispatcher.removeEventListener('test', fn);
    dispatcher.dispatchEvent('test');
    expect(event).toBe(1);
});

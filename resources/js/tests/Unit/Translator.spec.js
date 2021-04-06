import Translator from './../../Support/Translator.js';

test('it translates strings', () => {
    const translator = new Translator({
        'Total :items': 'Összesen :items',
    });

    expect(translator.__('Total :items', { items: 6 })).toBe('Összesen 6');
    expect(translator.__('Fake')).toBe('Fake');
});

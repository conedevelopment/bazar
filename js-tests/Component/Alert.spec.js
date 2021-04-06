import { shallowMount } from '@vue/test-utils';
import Alert from './../../resources/js/Components/Alert.vue';

describe('Alert.vue', () => {
    const wrapper = shallowMount(Alert, {
        propsData: {
            type: 'custom',
            closable: true,
        },
    });

    test('it can have different types', () => {
        expect(wrapper.element.classList.contains('alert-custom')).toBe(true);
        expect(wrapper.element.classList.contains('alert-dismissible')).toBe(true);
    });

    test('it can be open and closed', () => {
        expect(wrapper.vm.isOpen).toBe(true);

        wrapper.vm.close();
        expect(wrapper.vm.isOpen).toBe(false);

        wrapper.vm.open();
        expect(wrapper.vm.isOpen).toBe(true);

        wrapper.vm.toggle();
        expect(wrapper.vm.isOpen).toBe(false);
    });
});

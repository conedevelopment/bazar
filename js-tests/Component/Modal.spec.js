import { shallowMount } from '@vue/test-utils';
import Manager from './../../resources/js/Components/Modal';

describe('Modal.vue', () => {
    const wrapper = shallowMount(Manager, {
        propsData: {
            title: 'Test',
        },
    });

    test('it can be open and closed', () => {
        expect(wrapper.vm.isOpen).toBe(false);

        wrapper.vm.open();
        expect(wrapper.vm.isOpen).toBe(true);

        wrapper.vm.close();
        expect(wrapper.vm.isOpen).toBe(false);

        wrapper.vm.toggle();
        expect(wrapper.vm.isOpen).toBe(true);
    });
});

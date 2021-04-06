import { config } from '@vue/test-utils';

config.global.mocks = {
    __: () => jest.fn(),

    $dispatcher: {
        on: () => jest.fn(),
        once: () => jest.fn(),
        off: () => jest.fn(),
        emit: () => jest.fn(),
    },

    $http: {
        get: () => jest.fn(),
        post: () => jest.fn(),
        put: () => jest.fn(),
        patch: () => jest.fn(),
        delete: () => jest.fn(),
        head: () => jest.fn(),
    },

    $inertia: {
        //
    },
};

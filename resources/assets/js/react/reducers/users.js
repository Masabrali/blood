import store from '../store';

const users = [
    { id: 1, name: 'Masasi' },
    { id: 2, name: 'Moses' },
    { id: 3, name: 'Igogo' },
    { id: 4, name: 'Meshack' },
    { id: 5, name: 'Magreth' }
];

export default function (state = users, action = {}) {

    switch (action.type) {

        case 'USER_ADDED':
            return [
                ...state,
                action.user
            ]
            break;
    }

    return state;
}

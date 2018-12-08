export default function(state = [], action = {}) {

    switch (action.type) {

        case 'GAME_ADDED':
            return [
                action.game,
                ...state
            ]
            break;

        case 'GAME_UPDATED':
            return state.map( item => {
                return (item.id === action.game.id)? action.game : item;
            });
            break;

        case 'GAME_DELETED':
            return state.filter( item => item.id !== action.game.id );
            break;

        case 'GAMES_FETCHED':
            return action.games;
            break;

        case 'GAME_FETCHED':
            const index = state.findIndex( item => item.id === action.game.id )

            if (index > -1)
                return state.map( item => {
                    return (item.id === action.game.id)? action.game : item;
                });
            else
                return [
                    action.game,
                    ...state
                ];

            break;

        default: return state;
    }
}

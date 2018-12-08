// Import combineReducers
import { combineReducers } from 'redux';

// Import reducers
import users from './users';
import activeUser from './activeUser';
import games from './games';

// Combiner all reducers
const rootReducer = combineReducers({
    users: users,
    activeUser: activeUser,
    games: games
});

export default rootReducer;

import setGames from './setGames';

export default function () {
    return dispatch => {
        fetch('/api/games')
            .then( response => response.json() )
            .then( data => dispatch(setGames(data) ))
    };
}

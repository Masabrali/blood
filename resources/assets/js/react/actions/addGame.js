export default function addGame(game) {
    return {
        type: 'GAME_ADDED',
        game
    };
}

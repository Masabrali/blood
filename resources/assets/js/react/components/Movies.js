import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Movies extends Component {

    constructor(props) {

        super(props);

        this.state = {
            movies: [
              { title: "Avatar", genre: "Animation" },
              { title: "Titanic", genre: "Romantic" },
              { title: "Wall Street", genre: "Documentary" },
              { title: "Big Short", 'genre': "Documentary" }
            ]
        };

        this.handleSubmit = this.handleSubmit.bind(this);

    }

    handleSubmit(e) {

        e.preventDefault();

        var movies = this.state.movies;

        if (this.refs.newTitle.value != '' && this.refs.newGenre.value != '') {
            movies.push({ title: this.refs.newTitle.value, genre: this.refs.newGenre.value });

            this.setState({ movies: movies });
        }

        e.target.reset();

        return false;
    }

    render () {

        return (

            <div className="container movies">
                <br />
                <div className="row">
                    {
                        this.state.movies.map((movie, i) =>
                            <div key={i} className="movie col-md-2 mb-2">
                                <h3 className="name">{ movie.title }</h3>
                                <h5 className="genre">{ movie.genre }</h5>
                            </div>
                        )
                    }
                </div>
                <br />
                <div className="container">
                    <h3>Add Movie</h3>
                    <form className="form" onSubmit={this.handleSubmit}>
                        <label className="form-label">Title</label>
                        <input ref="newTitle" className="form-control" type="text" name="title" />
                        <br />
                        <label className="form-label">Genre</label>
                        <input ref="newGenre" className="form-control" type="text" name="genre" />
                        <br />
                        <button className="btn btn-primary" type="submit">Add Movie</button>
                    </form>
                </div>
                <br />
            </div>

        );
    }
};

import React, { Component } from 'react';
import ReactDOM from 'react-dom';

import Comments from './Comments';

export default class Board extends Component {
    constructor(props) {
        super(props);

        this.state = {
            comments: [
                'I like Milk',
                'Want to get some icecream',
                'Ok, thats enough comments'
            ]
        }

        this.add = this.add.bind(this);
        this.comment = this.comment.bind(this);
        this.update = this.update.bind(this);
        this.remove = this.remove.bind(this);
    }

    add(e) {

        e.preventDefault();

        var comments = this.state.comments;

        comments.push(e.target.comment.value);

        this.setState({ comments: comments });

        e.target.reset();

        return false;
    }

    update(text, index) {

        var comments = this.state.comments;

        comments[index] = text;

        this.setState({ comments: comments });
    }

    remove(index) {

        var comments = this.state.comments;

        comments.splice(index, 1);

        this.setState({ comments: comments });
    }

    comment(comment, i) {
        return (
            <Comments key={i} index={i} update={ this.update } remove={ this.remove }>
                { comment }
            </Comments>
        );
    }

    render() {
        return (
            <div className="container board">
                <div class="comments">
                    { this.state.comments.map(this.comment) }
                </div>
                <div className="new_comment">
                    <form className="form" onSubmit={ this.add }>
                        <label className="form-label">Add Comment</label>
                        <br />
                        <textarea className="textarea d-block w-100 p-2 mb-2" name="comment" placeholder="Your comment"></textarea>
                        <div className="container p-1">
                            <button type="submit" className="btn btn-success float-right">Comment</button>

                            <div className="clearfix"></div>
                        </div>
                    </form>
                </div>
            </div>
        )
    }
}

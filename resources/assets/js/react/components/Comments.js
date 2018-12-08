import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class Comments extends Component {

    constructor(props) {

        super(props);

        this.state = { editing: false };

        this.edit = this.edit.bind(this);
        this.save = this.save.bind(this);
        this.remove = this.remove.bind(this);
    }

    edit(e) {
        this.setState({ editing: true });
    }

    save(e) {

        e.preventDefault();

        this.props.update(this.refs.newComment.value, this.props.index);

        this.setState({ editing: false });

        return false;
    }

    remove(e) {

        this.props.remove(this.props.index);

    }

    renderNormal() {
        return (
            <div className="container comments">
                <div className="border border-info p-2 mb-2 text" onDoubleClick={ this.edit }>{ this.props.children }</div>
                <div className="container p-1">
                    <button onClick={ this.remove } className="btn btn-danger float-right ml-1">Remove</button>
                    <button onClick={ this.edit } className="btn btn-warning float-right mr-1">Edit</button>

                    <div className="container clearfix"></div>
                </div>
                <br />
            </div>
        )
    }

    renderForm() {
        return (
            <div className="container comments">
                <form className="form" onSubmit={ this.save }>
                    <textarea ref="newComment" name="comment" className="border border-info p-2 mb-2 text d-block w-100" defaultValue={ this.props.children }></textarea>
                    <div className="container p-1">
                        <button className="btn btn-success float-right mr-1">Save</button>

                        <div className="container clearfix"></div>
                    </div>
                </form>
                <br />
            </div>
        )
    }

    render() {
        if (this.state.editing) return this.renderForm();
        else return this.renderNormal();
    }

}

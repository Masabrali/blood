import React, { Component } from 'react';
import ReactDOM from 'react-dom';

export default class CheckBox extends Component {

    constructor(props) {

        super(props);

        this.state = { count: props.initialCount, checked: true };

        this.handleChange = this.handleChange.bind(this);
    }

    handleChange(event) {
        this.setState({ checked: !this.state.checked });
    }

    render() {

        var msg;

        if (this.state.checked) msg = "Checked";
        else msg = "Unchecked";

        return (
            <div className="container">
                <input type="checkbox" defaultChecked={ this.state.checked } onChange={ this.handleChange } />
                <p>Checkbox is { msg }</p>
            </div>
        );
    }
}

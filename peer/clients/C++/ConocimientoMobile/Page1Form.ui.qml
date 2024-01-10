import QtQuick 2.7
import QtQuick.Controls 2.0
import QtQuick.Layouts 1.3

Item {
    Button {
        id: button
        x: 20
        y: 121
        width: 150
        height: 34
        text: qsTr("Tecnología y Salud")
        checkable: false
    }

    Button {
        id: button1
        x: 21
        y: 188
        text: qsTr("Alimentos y Nutrición")
    }

    Button {
        id: button2
        x: 21
        y: 260
        text: qsTr("Energía")
    }

    Button {
        id: button3
        x: 21
        y: 336
        text: qsTr("Idioma")
    }

    Button {
        id: button4
        x: 24
        y: 405
        text: qsTr("Ajustes")
    }

    Frame {
        id: frame
        x: 207
        y: 119
        width: 254
        height: 675
    }

    Frame {
        id: frame1
        x: 19
        y: 3
        width: 442
        height: 55
    }
}

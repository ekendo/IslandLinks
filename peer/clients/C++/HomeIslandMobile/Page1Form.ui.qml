import QtQuick 2.7
import QtQuick.Controls 2.0
import QtQuick.Layouts 1.3

Item {
    property alias button1: button1
    width: 480
    height: 640

    Button {
        id: button7
        x: 9
        y: 310
        width: 106
        text: qsTr("Products")
    }

    Button {
        id: button3
        x: 9
        y: 160
        width: 106
        text: qsTr("Content")
    }

    Button {
        id: button1
        x: 9
        y: 60
        width: 106
        text: qsTr("Profile")
    }

    Button {
        id: button2
        x: 9
        y: 110
        width: 106
        text: qsTr("Tags")
    }

    TabBar {
        id: tabBar
        x: 135
        y: 60
        width: 298
        height: 558
    }

    Button {
        id: button4
        x: 9
        y: 210
        width: 106
        text: qsTr("Apps")
    }

    Button {
        id: button5
        x: 9
        y: 260
        width: 106
        text: qsTr("Communities")
    }

    Text {
        id: text1
        x: 92
        y: 20
        width: 298
        height: 29
        text: qsTr("Home Island Mobile v0.0.3")
        horizontalAlignment: Text.AlignHCenter
        font.pixelSize: 14
    }

    Button {
        id: button
        x: 9
        y: 360
        width: 106
        text: qsTr("Ads")
    }

    Image {
        id: image
        x: 9
        y: 410
        width: 106
        height: 106
        source: "qrc:/qtquickplugin/images/template_image.png"
    }

    Text {
        id: status
        x: 9
        y: 570
        width: 106
        height: 15
        text: qsTr("Text")
        font.pixelSize: 12
    }
}

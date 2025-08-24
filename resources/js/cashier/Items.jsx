import { Card, Col, Modal, Row } from 'antd'
import React, { useEffect, useState } from 'react'
import { Colors } from '../utils/Colors';
import TextArea from 'antd/es/input/TextArea';
import { CheckCircleTwoTone } from '@ant-design/icons';

const Items = ({ datas = [], setAddToCart = {}, remarks }) => {
    const [isModalOpen, setModalOpen] = useState(false);
    const [singleData, setSingleData] = useState({});
    const [btnDisabled, setBtnDisabled] = useState(true);

    const [selectedItem, setSelectedItem] = useState({
        id: '',
        name: '',
        category: '',
        attribute: '',
        price: '',
        quantity: 0,
        subtotal: 0,
        noted: '',
        remarks: ''
    });

    console.log(selectedItem);

    // filter single data dan buka modal
    const handleSelected = (id) => {
        const data = datas.find((item) => item.id == id);
        setModalOpen(true);
        setSingleData(data);
        setSelectedItem({
            id: data.id,
            name: data.name,
            category: data.category,
            attribute: '',
            price: '',
            quantity: 1,
            subtotal: 0,
            noted: ''
        });
    }

    const handleSelectedPrice = (attribute, price) => {
        setSelectedItem((prev) => {
            return ({
                ...prev,
                attribute: attribute,
                subtotal: price,
                price: price
            })
        });
    }

    const handleSelectedNoted = (noted) => {
        setSelectedItem((prev) => {
            return ({
                ...prev,
                noted: noted
            })
        });
    }

    const handleSelectedRemarks = (remarkId, checked) => {
        setSelectedItem((prev) => {
            let updatedRemarks = prev.remarks || [];

            if (checked) {
                // tambahkan remark jika dicentang
                updatedRemarks = [...updatedRemarks, remarkId];
            } else {
                // hapus remark jika di-uncheck
                updatedRemarks = updatedRemarks.filter((id) => id !== remarkId);
            }

            return {
                ...prev,
                remarks: updatedRemarks
            };
        });
    };

    const handleAddToCart = () => {
        setAddToCart(selectedItem);
        setSingleData({});
        setSelectedItem({
            id: '',
            name: '',
            category: '',
            attribute: '',
            price: '',
            quantity: 0,
            subtotal: 0,
            noted: ''
        });
        setModalOpen(false);
    }

    useEffect(() => {
        setBtnDisabled(!(selectedItem.price));

    }, [selectedItem.price]);


    return (
        <div style={{ height: 540, overflowY: 'scroll', overflowX: 'hidden' }}>
            <Row gutter={[8, 8]}>
                {
                    datas.map((val, i) => {
                        return (
                            <Col key={i} xs={12} sm={8} xl={6}>
                                <Card style={{ cursor: 'pointer' }} onClick={() => handleSelected(val.id)}>
                                    <h5 style={{ margin: 0, padding: 0, fontWeight: 'bold', fontSize: 11, letterSpacing: .5 }}>{val.category}</h5>
                                    <h4 style={{ margin: 0, padding: 0, fontWeight: 'normal', letterSpacing: .5 }}>{val.name}</h4>
                                    <Card style={{ marginTop: 6 }}>
                                        {
                                            val.attribute.length > 0 &&
                                            val.attribute.map((att, id) => {
                                                return (
                                                    <div key={id} style={{ display: 'flex', alignItems: 'center', gap: 4, flexDirection: 'row', justifyContent: 'space-between', borderBottomWidth: 1, borderBottomStyle: 'dashed', borderBottomColor: Colors.gray400, paddingBottom: 2, marginBottom: 2 }}>
                                                        <h6 style={{ margin: 0, padding: 0, fontWeight: 'normal', fontSize: 12, letterSpacing: .5, color: Colors.primary }}>{att.name}</h6>
                                                        <h6 style={{ margin: 0, padding: 0, fontWeight: 'bold', fontSize: 12, color: Colors.primary }}>Rp {att.price}</h6>
                                                    </div>
                                                )
                                            })
                                        }
                                    </Card>
                                </Card>
                            </Col>
                        )
                    })
                }
            </Row>

            <Modal
                title={singleData?.category + ' - ' + singleData?.name}
                closable={{ 'aria-label': 'Custom Close Button' }}
                open={isModalOpen}
                onCancel={() => setModalOpen(false)}
                footer={() => {
                    return (
                        <button type='button'
                            disabled={btnDisabled}
                            onClick={handleAddToCart}
                            style={{
                                cursor: 'pointer',
                                backgroundColor: btnDisabled ? Colors.gray500 : Colors.primary,
                                border: 0,
                                color: 'white',
                                paddingLeft: 12,
                                paddingRight: 12,
                                paddingTop: 6,
                                paddingBottom: 6,
                                borderRadius: 6
                            }}
                        >
                            Tambahkan
                        </button>
                    )
                }}
            >
                <div style={{ marginTop: 32 }}>
                    <div style={{ marginBottom: 22 }}>
                        <h5 style={{ margin: 0, padding: 0, fontWeight: 500 }}>Harga</h5>
                        {
                            Object.keys(singleData).length > 0 &&
                            singleData.attribute.map((val, i) => {
                                return (
                                    <div
                                        key={i}
                                        onClick={() => handleSelectedPrice(val.name, val.price)}
                                        style={{
                                            borderWidth: val.price == selectedItem.price ? 2 : 1,
                                            borderColor: val.price == selectedItem.price ? Colors.primary : Colors.gray400,
                                            borderStyle: 'solid',
                                            padding: 8,
                                            borderRadius: 6,
                                            marginBottom: 8,
                                            cursor: 'pointer'
                                        }}>

                                        <div style={{ display: 'flex', flexDirection: 'row', justifyContent: "space-between" }}>
                                            <div>
                                                {val.name} - Rp {val.price}
                                            </div>
                                            {
                                                val.price == selectedItem.price &&
                                                <CheckCircleTwoTone />
                                            }
                                        </div>
                                    </div>
                                )
                            })

                        }
                    </div>

                    <div>
                        {remarks.map((val, i) => (
                            <label key={i} style={{ display: "block", marginBottom: "4px" }}>
                                <input
                                    type="checkbox"
                                    name="remarks[]"
                                    value={val.id}
                                    checked={selectedItem?.remarks?.includes(val.id) || false}
                                    onChange={(e) => handleSelectedRemarks(val.id, e.target.checked)}
                                />{" "}
                                {val.name}
                            </label>
                        ))}
                    </div>


                    <div style={{ marginBottom: 22 }}>
                        <h5 style={{ margin: 0, padding: 0, fontWeight: 500 }}>Catatan</h5>
                        <TextArea
                            rows={4}
                            style={{ borderColor: Colors.gray400 }}
                            placeholder='Misalnya: Baju sudah ada goresan...'
                            defaultValue={''}
                            onChange={(e) => handleSelectedNoted(e.target.value)}
                        >
                        </TextArea>
                    </div>
                </div>
            </Modal>
        </div>
    )
}

export default Items
